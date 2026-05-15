<?php $page_title = "About AdmissionSeason | India's Smartest College Discovery Platform"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <meta name="description" content="Learn about AdmissionSeason — our mission, team, and vision for transforming college admissions in India through AI and data.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        <?php include 'assets/css/style.css'; ?>
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
        .float { animation: float 4s ease-in-out infinite; }
    </style>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="antialiased bg-[var(--bg-primary)] text-white font-['Inter']">
<?php include 'includes/navbar.php'; ?>

<div class="min-h-screen pt-16">

  <!-- Hero -->
  <div class="relative bg-gradient-to-b from-[var(--bg-secondary)] to-[var(--bg-primary)] border-b border-[var(--border)] py-20 overflow-hidden">
    <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-indigo-500/10 blur-[140px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-purple-500/10 blur-[120px] rounded-full pointer-events-none"></div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 text-center relative z-10">
      <div class="inline-flex items-center gap-2 px-4 py-1.5 mb-6 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-bold uppercase tracking-widest">
        <i data-lucide="heart" class="w-3.5 h-3.5"></i> Our Story
      </div>
      <h1 class="text-4xl sm:text-5xl font-black text-white mb-5 leading-tight">
        Helping Every Indian Student<br><span class="gradient-text">Find Their Right College</span>
      </h1>
      <p class="text-[var(--text-secondary)] text-lg max-w-2xl mx-auto leading-relaxed">
        AdmissionSeason was born from one simple frustration — the college admission process in India is broken. 
        We're here to fix it with AI, data, and a deep passion for education.
      </p>
    </div>
  </div>

  <!-- Mission + Vision -->
  <div class="max-w-6xl mx-auto px-4 sm:px-6 py-16">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-16">
      <?php $mvv = [
        ['icon'=>'target','title'=>'Our Mission','text'=>'To democratize college admissions in India by providing every student — regardless of city, background, or income — access to unbiased, data-driven guidance.','color'=>'text-indigo-400','border'=>'border-indigo-500/20','bg'=>'bg-indigo-500/10'],
        ['icon'=>'eye','title'=>'Our Vision','text'=>'A future where no Indian student makes a wrong college choice due to lack of information. Where admissions are transparent, fair, and driven by merit.','color'=>'text-emerald-400','border'=>'border-emerald-500/20','bg'=>'bg-emerald-500/10'],
        ['icon'=>'star','title'=>'Our Values','text'=>'Transparency, student-first thinking, data integrity, and continuous innovation. We never take commissions from colleges for rankings or recommendations.','color'=>'text-amber-400','border'=>'border-amber-500/20','bg'=>'bg-amber-500/10'],
      ]; foreach ($mvv as $item): ?>
      <div class="glass rounded-2xl border <?= $item['border'] ?> p-7 text-center">
        <div class="w-14 h-14 mx-auto rounded-2xl <?= $item['bg'] ?> border <?= $item['border'] ?> flex items-center justify-center mb-4 float">
          <i data-lucide="<?= $item['icon'] ?>" class="w-7 h-7 <?= $item['color'] ?>"></i>
        </div>
        <h2 class="font-bold text-white text-lg mb-3"><?= $item['title'] ?></h2>
        <p class="text-sm text-[var(--text-secondary)] leading-relaxed"><?= $item['text'] ?></p>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Stats -->
    <div class="glass rounded-3xl border border-[var(--border)] p-10 mb-16">
      <h2 class="text-2xl font-bold text-white text-center mb-10">AdmissionSeason by Numbers</h2>
      <div class="grid grid-cols-2 sm:grid-cols-4 gap-8 text-center">
        <?php $nums = [
          ['val'=>'10L+','label'=>'Students Helped','icon'=>'users','color'=>'text-indigo-400'],
          ['val'=>'30K+','label'=>'Colleges Listed','icon'=>'building-2','color'=>'text-emerald-400'],
          ['val'=>'350+','label'=>'Exams Tracked','icon'=>'pen-line','color'=>'text-amber-400'],
          ['val'=>'₹50Cr+','label'=>'Scholarships Found','icon'=>'award','color'=>'text-purple-400'],
        ]; foreach ($nums as $n): ?>
        <div>
          <div class="text-4xl font-black <?= $n['color'] ?> mb-1"><?= $n['val'] ?></div>
          <div class="text-xs text-[var(--text-muted)] uppercase tracking-wider"><?= $n['label'] ?></div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Team -->
    <div class="mb-16">
      <h2 class="text-2xl font-bold text-white text-center mb-10">Meet the Team</h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
        <?php $team = [
          ['name'=>'Madhav Arora','role'=>'Founder & CEO','emoji'=>'👨‍💻','desc'=>'IIT Delhi dropout who went on to build two edtech startups. Passionate about making quality education accessible.'],
          ['name'=>'Priya Nair','role'=>'CTO','emoji'=>'👩‍💻','desc'=>'Ex-Google engineer with 8 years in ML. Leads our AI Counselor and recommendation engine.'],
          ['name'=>'Rohan Mehta','role'=>'Head of Data','emoji'=>'📊','desc'=>'Former NIRF data analyst. Ensures all 30,000+ college profiles are accurate, updated, and verified.'],
          ['name'=>'Sneha Gupta','role'=>'Head of Marketing','emoji'=>'📱','desc'=>'Ex-Byju\'s growth lead. Built our user base from 0 to 10 lakh students in 18 months.'],
          ['name'=>'Arjun Kapoor','role'=>'Design Lead','emoji'=>'🎨','desc'=>'Award-winning UX designer who believes every student deserves a beautiful, intuitive experience.'],
          ['name'=>'Divya Sharma','role'=>'Student Success','emoji'=>'🌟','desc'=>'Former IIT counselor. Personally mentors 500+ students every admission season.'],
        ]; foreach ($team as $t): ?>
        <div class="glass rounded-2xl border border-[var(--border)] p-6 text-center hover:border-indigo-500/30 transition-colors group">
          <div class="text-4xl mb-3"><?= $t['emoji'] ?></div>
          <h3 class="font-bold text-white group-hover:text-indigo-400 transition-colors"><?= $t['name'] ?></h3>
          <p class="text-xs text-indigo-400 font-medium mb-3"><?= $t['role'] ?></p>
          <p class="text-xs text-[var(--text-secondary)] leading-relaxed"><?= $t['desc'] ?></p>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- CTA -->
    <div class="glass rounded-3xl border border-indigo-500/20 bg-indigo-500/5 p-10 text-center">
      <h2 class="text-2xl font-bold text-white mb-3">Ready to Find Your Perfect College?</h2>
      <p class="text-[var(--text-secondary)] mb-6 max-w-md mx-auto">Join 10 lakh+ students who trust AdmissionSeason for their college journey.</p>
      <div class="flex flex-col sm:flex-row gap-4 justify-center">
        <a href="colleges.php" class="btn-primary inline-flex items-center gap-2 px-8 py-3 rounded-xl font-semibold">
          <i data-lucide="search" class="w-4 h-4"></i> Explore Colleges
        </a>
        <a href="ai-counselor.php" class="glass inline-flex items-center gap-2 px-8 py-3 rounded-xl font-semibold border border-[var(--border)] hover:bg-white/5 transition-colors">
          <i data-lucide="bot" class="w-4 h-4 text-indigo-400"></i> Ask AI Counselor
        </a>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
<script>lucide.createIcons();</script>
</body>
</html>
