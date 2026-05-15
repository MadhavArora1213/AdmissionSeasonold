<?php
require_once '../AdmissionSeason/admin/includes/db.php';
$page_title = "JEE / NEET Rank Predictor 2026 | AdmissionSeason";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <meta name="description" content="Predict your JEE Main, NEET, CAT rank from your score instantly. See which colleges you can get into.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        <?php include 'assets/css/style.css'; ?>

        @keyframes countUp {
            from { opacity: 0; transform: translateY(20px) scale(0.8); }
            to   { opacity: 1; transform: translateY(0)  scale(1); }
        }
        .count-anim { animation: countUp 0.6s cubic-bezier(.17,.67,.38,1.2) forwards; }

        @keyframes barFill {
            from { width: 0%; }
        }
        .bar-fill { animation: barFill 1.2s ease-out forwards; }

        .range-thumb::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 22px; height: 22px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            cursor: pointer;
            border: 3px solid white;
            box-shadow: 0 0 0 4px rgba(99,102,241,0.3);
        }
        .range-thumb::-moz-range-thumb {
            width: 22px; height: 22px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #a855f7);
            cursor: pointer;
            border: 3px solid white;
        }
    </style>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="antialiased bg-[var(--bg-primary)] text-white font-['Inter']">
<?php include 'includes/navbar.php'; ?>

<div class="min-h-screen pt-16">

  <!-- Hero -->
  <div class="bg-gradient-to-b from-[var(--bg-secondary)] to-[var(--bg-primary)] border-b border-[var(--border)] py-14 relative overflow-hidden">
    <div class="absolute top-0 right-1/4 w-96 h-96 bg-indigo-500/10 blur-[120px] rounded-full pointer-events-none"></div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 relative z-10 text-center">
      <div class="inline-flex items-center gap-2 px-4 py-1.5 mb-5 rounded-full bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 text-xs font-bold uppercase tracking-widest">
        <i data-lucide="trending-up" class="w-3.5 h-3.5"></i> AI Rank Predictor 2026
      </div>
      <h1 class="text-3xl sm:text-5xl font-bold text-white mb-4 tracking-tight">
        Predict Your Rank <span class="gradient-text">Instantly</span>
      </h1>
      <p class="text-[var(--text-secondary)] text-base sm:text-lg max-w-xl mx-auto">
        Enter your score and get your predicted rank for JEE Main, NEET, CAT & more — and discover which colleges you're eligible for.
      </p>
    </div>
  </div>

  <div class="max-w-5xl mx-auto px-4 sm:px-6 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

      <!-- ── Input Panel ── -->
      <div class="lg:col-span-2 space-y-6">
        <div class="glass rounded-2xl border border-[var(--border)] p-6">
          <h2 class="font-bold text-white text-lg mb-5 flex items-center gap-2">
            <i data-lucide="sliders-horizontal" class="w-5 h-5 text-indigo-400"></i> Your Details
          </h2>

          <!-- Exam Select -->
          <div class="mb-5">
            <label class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-2 block">Select Exam</label>
            <div class="grid grid-cols-2 gap-2" id="examBtns">
              <?php
              $exams = [
                ['id'=>'jee_main',  'label'=>'JEE Main',  'max'=>300, 'icon'=>'⚙️'],
                ['id'=>'neet',      'label'=>'NEET',      'max'=>720, 'icon'=>'🏥'],
                ['id'=>'cat',       'label'=>'CAT',       'max'=>228, 'icon'=>'📊'],
                ['id'=>'gate',      'label'=>'GATE',      'max'=>100, 'icon'=>'🔬'],
                ['id'=>'bitsat',    'label'=>'BITSAT',    'max'=>390, 'icon'=>'🎓'],
                ['id'=>'cuet',      'label'=>'CUET',      'max'=>800, 'icon'=>'🏛️'],
              ];
              foreach ($exams as $ex): ?>
              <button onclick="selectExam('<?= $ex['id'] ?>', <?= $ex['max'] ?>)"
                      id="btn_<?= $ex['id'] ?>"
                      class="exam-btn flex items-center gap-2 px-3 py-2.5 rounded-xl text-sm font-medium border transition-all
                             border-[var(--border)] text-[var(--text-secondary)] hover:border-indigo-500/50 hover:text-white bg-[var(--bg-secondary)]">
                <span class="text-lg"><?= $ex['icon'] ?></span> <?= $ex['label'] ?>
              </button>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Score Slider -->
          <div class="mb-5">
            <div class="flex justify-between items-center mb-2">
              <label class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider">Your Score</label>
              <span id="scoreDisplay" class="text-2xl font-bold text-indigo-400">180</span>
            </div>
            <input type="range" id="scoreSlider" min="0" max="300" value="180" step="1"
              oninput="updateScore(this.value)"
              class="range-thumb w-full h-2 appearance-none bg-[var(--bg-secondary)] border border-[var(--border)] rounded-full outline-none cursor-pointer accent-indigo-500">
            <div class="flex justify-between text-[10px] text-[var(--text-muted)] mt-1">
              <span>0</span><span id="maxLabel">300</span>
            </div>
          </div>

          <!-- Category -->
          <div class="mb-5">
            <label class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-2 block">Category</label>
            <div class="relative">
              <select id="categorySelect" class="w-full appearance-none px-4 py-2.5 bg-[var(--bg-secondary)] border border-[var(--border)] rounded-xl text-sm text-white outline-none focus:border-indigo-500/60 cursor-pointer">
                <option value="general">General / EWS</option>
                <option value="obc">OBC-NCL</option>
                <option value="sc">SC</option>
                <option value="st">ST</option>
              </select>
              <i data-lucide="chevron-down" class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--text-muted)] pointer-events-none"></i>
            </div>
          </div>

          <!-- Predict Button -->
          <button onclick="predictRank()"
            class="w-full btn-primary py-3.5 rounded-xl font-bold flex items-center justify-center gap-2 text-base group">
            <i data-lucide="sparkles" class="w-5 h-5 group-hover:rotate-12 transition-transform"></i>
            Predict My Rank
          </button>
        </div>

        <!-- Tips Card -->
        <div class="glass rounded-2xl border border-amber-500/20 p-5 bg-amber-500/5">
          <h3 class="font-bold text-amber-400 mb-3 flex items-center gap-2 text-sm">
            <i data-lucide="lightbulb" class="w-4 h-4"></i> Pro Tips
          </h3>
          <ul class="space-y-2 text-xs text-[var(--text-secondary)]">
            <li class="flex items-start gap-2"><i data-lucide="check-circle" class="w-3.5 h-3.5 text-amber-400 flex-shrink-0 mt-0.5"></i> Rank prediction is based on previous year data trends</li>
            <li class="flex items-start gap-2"><i data-lucide="check-circle" class="w-3.5 h-3.5 text-amber-400 flex-shrink-0 mt-0.5"></i> Actual rank may vary ±15% based on paper difficulty</li>
            <li class="flex items-start gap-2"><i data-lucide="check-circle" class="w-3.5 h-3.5 text-amber-400 flex-shrink-0 mt-0.5"></i> OBC/SC/ST get relaxed cutoffs in government colleges</li>
          </ul>
        </div>
      </div>

      <!-- ── Result Panel ── -->
      <div class="lg:col-span-3">

        <!-- Initial State -->
        <div id="initialState" class="glass rounded-2xl border border-dashed border-[var(--border)] h-full flex items-center justify-center p-12">
          <div class="text-center">
            <div class="w-20 h-20 mx-auto rounded-full bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-center mb-5">
              <i data-lucide="bar-chart-3" class="w-10 h-10 text-indigo-400 opacity-50"></i>
            </div>
            <h3 class="font-bold text-white text-lg mb-2">Select exam & enter score</h3>
            <p class="text-sm text-[var(--text-secondary)] max-w-xs mx-auto">Click "Predict My Rank" to see your predicted rank, percentile, and eligible colleges.</p>
          </div>
        </div>

        <!-- Result State (hidden initially) -->
        <div id="resultState" class="hidden space-y-6">

          <!-- Rank Card -->
          <div class="glass rounded-2xl border border-indigo-500/30 p-7 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-indigo-500/5 to-purple-500/5 pointer-events-none"></div>
            <div class="relative z-10">
              <div class="flex flex-col sm:flex-row items-start sm:items-center gap-6 mb-6">
                <div class="text-center">
                  <div class="text-xs text-[var(--text-muted)] uppercase tracking-wider mb-1">Predicted Rank</div>
                  <div id="rankResult" class="text-5xl font-black text-white count-anim">—</div>
                  <div class="text-xs text-indigo-400 font-medium mt-1" id="examLabel">—</div>
                </div>
                <div class="flex-1 grid grid-cols-3 gap-4">
                  <div class="text-center glass rounded-xl p-4 border border-[var(--border)]">
                    <div class="text-xs text-[var(--text-muted)] mb-1">Percentile</div>
                    <div id="percentileResult" class="text-xl font-bold text-emerald-400">—</div>
                  </div>
                  <div class="text-center glass rounded-xl p-4 border border-[var(--border)]">
                    <div class="text-xs text-[var(--text-muted)] mb-1">Your Score</div>
                    <div id="scoreResult" class="text-xl font-bold text-amber-400">—</div>
                  </div>
                  <div class="text-center glass rounded-xl p-4 border border-[var(--border)]">
                    <div class="text-xs text-[var(--text-muted)] mb-1">Chance</div>
                    <div id="chanceResult" class="text-xl font-bold text-indigo-400">—</div>
                  </div>
                </div>
              </div>

              <!-- Score bar -->
              <div>
                <div class="flex justify-between text-xs text-[var(--text-muted)] mb-2">
                  <span>Score Percentile</span>
                  <span id="barPercent">0%</span>
                </div>
                <div class="h-3 bg-[var(--bg-secondary)] rounded-full overflow-hidden border border-[var(--border)]">
                  <div id="scoreBar" class="h-full rounded-full bar-fill bg-gradient-to-r from-indigo-500 to-purple-500" style="width:0%"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Eligible Colleges -->
          <div class="glass rounded-2xl border border-[var(--border)] overflow-hidden">
            <div class="px-6 py-4 border-b border-[var(--border)] flex items-center justify-between">
              <h3 class="font-bold text-white flex items-center gap-2">
                <i data-lucide="university" class="w-5 h-5 text-indigo-400"></i>
                Colleges You Can Get Into
              </h3>
              <span id="collegeCount" class="text-xs text-[var(--text-muted)]"></span>
            </div>
            <div id="collegesList" class="divide-y divide-[var(--border)]">
              <!-- Populated by JS -->
            </div>
          </div>

          <!-- CTA -->
          <div class="grid grid-cols-2 gap-4">
            <a href="colleges.php" class="flex items-center justify-center gap-2 py-3 rounded-xl glass border border-[var(--border)] text-sm font-medium hover:bg-white/5 transition-colors">
              <i data-lucide="search" class="w-4 h-4"></i> Browse All Colleges
            </a>
            <a href="ai-counselor.php" class="flex items-center justify-center gap-2 py-3 rounded-xl btn-primary text-sm font-medium">
              <i data-lucide="bot" class="w-4 h-4"></i> Talk to AI Counselor
            </a>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
lucide.createIcons();

// ── Exam Config ──────────────────────────────────────────────
const examConfig = {
  jee_main:  { label: 'JEE Main 2026',  max: 300, totalCandidates: 1200000, formula: s => Math.max(1, Math.round(1200000 * Math.pow((300 - s) / 300, 2.8))) },
  neet:      { label: 'NEET UG 2026',   max: 720, totalCandidates: 2000000, formula: s => Math.max(1, Math.round(2000000 * Math.pow((720 - s) / 720, 3.2))) },
  cat:       { label: 'CAT 2025',       max: 228, totalCandidates: 300000,  formula: s => Math.max(1, Math.round(300000  * Math.pow((228 - s) / 228, 2.5))) },
  gate:      { label: 'GATE 2026',      max: 100, totalCandidates: 900000,  formula: s => Math.max(1, Math.round(900000  * Math.pow((100 - s) / 100, 2.2))) },
  bitsat:    { label: 'BITSAT 2026',    max: 390, totalCandidates: 400000,  formula: s => Math.max(1, Math.round(400000  * Math.pow((390 - s) / 390, 2.6))) },
  cuet:      { label: 'CUET UG 2026',   max: 800, totalCandidates: 1500000, formula: s => Math.max(1, Math.round(1500000 * Math.pow((800 - s) / 800, 2.0))) },
};

// Category rank relaxation factor
const catFactor = { general: 1, obc: 1.35, sc: 2.8, st: 4.5 };

let selectedExam = 'jee_main';
let selectedMax  = 300;

function selectExam(id, max) {
  selectedExam = id;
  selectedMax  = max;
  document.getElementById('scoreSlider').max = max;
  document.getElementById('maxLabel').textContent = max;
  const score = Math.min(parseInt(document.getElementById('scoreSlider').value), max);
  document.getElementById('scoreSlider').value = score;
  updateScore(score);
  // Highlight selected button
  document.querySelectorAll('.exam-btn').forEach(b => {
    b.classList.remove('border-indigo-500', 'text-indigo-400', 'bg-indigo-500/10');
    b.classList.add('border-[var(--border)]', 'text-[var(--text-secondary)]', 'bg-[var(--bg-secondary)]');
  });
  const btn = document.getElementById('btn_' + id);
  btn.classList.add('border-indigo-500', 'text-indigo-400', 'bg-indigo-500/10');
  btn.classList.remove('border-[var(--border)]', 'text-[var(--text-secondary)]', 'bg-[var(--bg-secondary)]');
}

function updateScore(val) {
  document.getElementById('scoreDisplay').textContent = val;
}

function predictRank() {
  const score   = parseInt(document.getElementById('scoreSlider').value);
  const cat     = document.getElementById('categorySelect').value;
  const cfg     = examConfig[selectedExam];

  let rank       = cfg.formula(score);
  const factor   = catFactor[cat] || 1;
  rank           = Math.max(1, Math.round(rank / factor));
  const total    = cfg.totalCandidates;
  const pctile   = Math.max(0.01, Math.min(99.99, ((total - rank) / total) * 100)).toFixed(2);
  const chance   = pctile >= 95 ? 'Excellent' : pctile >= 80 ? 'Good' : pctile >= 60 ? 'Fair' : 'Low';
  const chColor  = pctile >= 95 ? 'text-emerald-400' : pctile >= 80 ? 'text-amber-400' : pctile >= 60 ? 'text-orange-400' : 'text-red-400';

  // Show results
  document.getElementById('initialState').classList.add('hidden');
  document.getElementById('resultState').classList.remove('hidden');
  document.getElementById('rankResult').textContent    = '#' + rank.toLocaleString('en-IN');
  document.getElementById('examLabel').textContent     = cfg.label;
  document.getElementById('percentileResult').textContent = pctile + '%';
  document.getElementById('scoreResult').textContent   = score + '/' + cfg.max;
  const cr = document.getElementById('chanceResult');
  cr.textContent = chance;
  cr.className   = 'text-xl font-bold ' + chColor;
  document.getElementById('barPercent').textContent    = pctile + '%';
  document.getElementById('scoreBar').style.width      = pctile + '%';

  // Eligible college bands based on rank
  renderColleges(rank, selectedExam);
  lucide.createIcons();
}

function renderColleges(rank, exam) {
  // Tier bands — static representative data mapped to rank ranges
  const bands = [
    { maxRank: 500,    tag: 'Top Govt', color: 'emerald', desc: 'IIT Bombay, IIT Delhi, IIT Madras, AIIMS Delhi' },
    { maxRank: 2000,   tag: 'Top Govt', color: 'emerald', desc: 'IIT Roorkee, IIT Kharagpur, NIT Trichy, IIT BHU' },
    { maxRank: 10000,  tag: 'Govt',     color: 'blue',    desc: 'Other NITs, BITS Pilani, IIIT Hyderabad, DTU' },
    { maxRank: 50000,  tag: 'Good',     color: 'indigo',  desc: 'VIT Vellore, SRM Chennai, Manipal, Amity Univ' },
    { maxRank: 200000, tag: 'Private',  color: 'purple',  desc: 'Chandigarh Univ, LPU, MIT Pune, Christ Univ' },
    { maxRank: 999999, tag: 'Private',  color: 'slate',   desc: 'Various Private Colleges across India' },
  ];

  const eligible = bands.filter(b => rank <= b.maxRank);
  const container = document.getElementById('collegesList');
  document.getElementById('collegeCount').textContent = eligible.length + ' tiers available';

  if (eligible.length === 0) {
    container.innerHTML = '<div class="p-8 text-center text-[var(--text-secondary)] text-sm"><i data-lucide="info" class="w-5 h-5 mx-auto mb-2 opacity-40"></i><p>With this score, target state-level or private colleges and aim to improve next year.</p></div>';
    return;
  }

  container.innerHTML = eligible.slice(0, 4).map((b, i) => `
    <div class="flex items-center gap-4 px-5 py-4 hover:bg-white/[0.02] transition-colors">
      <div class="w-8 h-8 rounded-lg bg-${b.color}-500/10 border border-${b.color}-500/20 flex items-center justify-center flex-shrink-0">
        <span class="text-${b.color}-400 font-bold text-xs">${i+1}</span>
      </div>
      <div class="flex-1">
        <div class="text-sm text-[var(--text-secondary)]">${b.desc}</div>
      </div>
      <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-${b.color}-500/10 text-${b.color}-400 border border-${b.color}-500/20 uppercase tracking-wider">${b.tag}</span>
    </div>
  `).join('');
}

// Initialize with JEE Main selected
selectExam('jee_main', 300);
</script>
</body>
</html>
