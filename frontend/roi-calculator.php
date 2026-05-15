<?php
require_once '../AdmissionSeason/admin/includes/db.php';

// Fetch colleges for dropdown
$collegelist = $pdo->query("SELECT id, name, city, type FROM colleges ORDER BY (nirf_rank IS NULL), nirf_rank ASC, name ASC LIMIT 100")->fetchAll(PDO::FETCH_ASSOC);

$page_title = "College ROI Calculator 2026 | AdmissionSeason";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <meta name="description" content="Calculate the Return on Investment (ROI) for any college. Compare fees vs expected salary and find out when you'll break even.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <style type="text/tailwindcss">
        <?php include 'assets/css/style.css'; ?>

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(16px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .slide-in { animation: slideIn 0.5s ease-out forwards; }

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

        /* Donut chart */
        .donut { transform: rotate(-90deg); }
        .donut-ring { fill: none; stroke-width: 18; }
    </style>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="antialiased bg-[var(--bg-primary)] text-white font-['Inter']">
<?php include 'includes/navbar.php'; ?>

<div class="min-h-screen pt-16">

  <!-- Hero -->
  <div class="bg-gradient-to-b from-[var(--bg-secondary)] to-[var(--bg-primary)] border-b border-[var(--border)] py-14 relative overflow-hidden">
    <div class="absolute top-0 left-1/3 w-96 h-96 bg-emerald-500/10 blur-[120px] rounded-full pointer-events-none"></div>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 relative z-10 text-center">
      <div class="inline-flex items-center gap-2 px-4 py-1.5 mb-5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold uppercase tracking-widest">
        <i data-lucide="calculator" class="w-3.5 h-3.5"></i> ROI Calculator 2026
      </div>
      <h1 class="text-3xl sm:text-5xl font-bold text-white mb-4 tracking-tight">
        Is Your College Worth The <span class="gradient-text">Investment?</span>
      </h1>
      <p class="text-[var(--text-secondary)] text-base sm:text-lg max-w-xl mx-auto">
        Calculate break-even point, lifetime earnings, and net ROI before you pay the first semester fee.
      </p>
    </div>
  </div>

  <div class="max-w-6xl mx-auto px-4 sm:px-6 py-12">
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-8">

      <!-- ── Input Panel ── -->
      <div class="lg:col-span-2 space-y-5">

        <div class="glass rounded-2xl border border-[var(--border)] p-6">
          <h2 class="font-bold text-white text-lg mb-5 flex items-center gap-2">
            <i data-lucide="settings-2" class="w-5 h-5 text-emerald-400"></i> Investment Details
          </h2>

          <!-- College Dropdown -->
          <div class="mb-5">
            <label class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-2 block">Select College (Optional)</label>
            <div class="relative">
              <select id="collegeSelect" onchange="prefillCollege(this.value)"
                class="w-full appearance-none px-4 py-2.5 bg-[var(--bg-secondary)] border border-[var(--border)] rounded-xl text-sm text-white outline-none focus:border-emerald-500/60 cursor-pointer">
                <option value="">— Enter manually —</option>
                <?php foreach ($collegelist as $c): ?>
                  <option value="<?= $c['id'] ?>" data-name="<?= htmlspecialchars($c['name']) ?>" data-type="<?= $c['type'] ?>">
                    <?= htmlspecialchars($c['name']) ?> (<?= $c['city'] ?>)
                  </option>
                <?php endforeach; ?>
              </select>
              <i data-lucide="chevron-down" class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-[var(--text-muted)] pointer-events-none"></i>
            </div>
          </div>

          <!-- Total Fees -->
          <div class="mb-5">
            <div class="flex justify-between items-center mb-2">
              <label class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider">Total Course Fees</label>
              <span class="text-emerald-400 font-bold text-sm" id="feesDisplay">₹8,00,000</span>
            </div>
            <input type="range" id="feesSlider" min="100000" max="10000000" step="50000" value="800000"
              oninput="updateDisplay()"
              class="range-thumb w-full h-2 appearance-none bg-[var(--bg-secondary)] rounded-full outline-none cursor-pointer">
            <div class="flex justify-between text-[10px] text-[var(--text-muted)] mt-1">
              <span>₹1L</span><span>₹1Cr</span>
            </div>
          </div>

          <!-- Living Costs -->
          <div class="mb-5">
            <div class="flex justify-between items-center mb-2">
              <label class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider">Monthly Living Cost</label>
              <span class="text-amber-400 font-bold text-sm" id="livingDisplay">₹10,000</span>
            </div>
            <input type="range" id="livingSlider" min="3000" max="60000" step="1000" value="10000"
              oninput="updateDisplay()"
              class="range-thumb w-full h-2 appearance-none bg-[var(--bg-secondary)] rounded-full outline-none cursor-pointer">
            <div class="flex justify-between text-[10px] text-[var(--text-muted)] mt-1">
              <span>₹3K</span><span>₹60K</span>
            </div>
          </div>

          <!-- Course Duration -->
          <div class="mb-5">
            <label class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider mb-2 block">Course Duration</label>
            <div class="grid grid-cols-4 gap-2">
              <?php foreach ([1, 2, 3, 4] as $yr): ?>
              <button onclick="setDuration(<?= $yr ?>)" id="dur_<?= $yr ?>"
                class="dur-btn py-2 rounded-xl text-sm font-bold border transition-all border-[var(--border)] text-[var(--text-secondary)] bg-[var(--bg-secondary)] hover:border-emerald-500/50">
                <?= $yr ?> yr
              </button>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Starting Salary -->
          <div class="mb-5">
            <div class="flex justify-between items-center mb-2">
              <label class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider">Expected Starting Salary (p.a.)</label>
              <span class="text-indigo-400 font-bold text-sm" id="salaryDisplay">₹6,00,000</span>
            </div>
            <input type="range" id="salarySlider" min="180000" max="10000000" step="60000" value="600000"
              oninput="updateDisplay()"
              class="range-thumb w-full h-2 appearance-none bg-[var(--bg-secondary)] rounded-full outline-none cursor-pointer">
            <div class="flex justify-between text-[10px] text-[var(--text-muted)] mt-1">
              <span>₹1.8L</span><span>₹1Cr</span>
            </div>
          </div>

          <!-- Salary Growth -->
          <div class="mb-6">
            <div class="flex justify-between items-center mb-2">
              <label class="text-xs font-semibold text-[var(--text-muted)] uppercase tracking-wider">Annual Salary Hike</label>
              <span class="text-purple-400 font-bold text-sm" id="hikeDisplay">8%</span>
            </div>
            <input type="range" id="hikeSlider" min="3" max="30" step="1" value="8"
              oninput="updateDisplay()"
              class="range-thumb w-full h-2 appearance-none bg-[var(--bg-secondary)] rounded-full outline-none cursor-pointer">
            <div class="flex justify-between text-[10px] text-[var(--text-muted)] mt-1">
              <span>3%</span><span>30%</span>
            </div>
          </div>

          <button onclick="calculateROI()"
            class="w-full btn-primary py-3.5 rounded-xl font-bold text-base flex items-center justify-center gap-2 group">
            <i data-lucide="zap" class="w-5 h-5 group-hover:text-yellow-300 transition-colors"></i>
            Calculate ROI
          </button>
        </div>
      </div>

      <!-- ── Results Panel ── -->
      <div class="lg:col-span-3 space-y-6">

        <!-- Initial -->
        <div id="roiInitial" class="glass rounded-2xl border border-dashed border-[var(--border)] flex items-center justify-center p-16 h-full">
          <div class="text-center">
            <div class="w-20 h-20 mx-auto rounded-full bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center mb-5">
              <i data-lucide="pie-chart" class="w-10 h-10 text-emerald-400 opacity-50"></i>
            </div>
            <h3 class="font-bold text-white text-lg mb-2">Enter your details</h3>
            <p class="text-sm text-[var(--text-secondary)] max-w-xs mx-auto">Adjust the sliders and click Calculate to see your college ROI, break-even timeline and 10-year earnings projection.</p>
          </div>
        </div>

        <!-- Results -->
        <div id="roiResults" class="hidden space-y-5 slide-in">

          <!-- Verdict Banner -->
          <div id="verdictBanner" class="rounded-2xl p-5 border flex items-center gap-4">
            <div id="verdictIcon" class="w-14 h-14 rounded-xl flex items-center justify-center text-3xl flex-shrink-0">🎯</div>
            <div>
              <div id="verdictTitle" class="font-black text-xl text-white"></div>
              <div id="verdictSub"   class="text-sm text-[var(--text-secondary)] mt-0.5"></div>
            </div>
          </div>

          <!-- Stats Grid -->
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="glass rounded-2xl border border-[var(--border)] p-5 text-center">
              <div class="text-xs text-[var(--text-muted)] mb-2 uppercase tracking-wider">Total Investment</div>
              <div id="totalInvest" class="text-xl font-black text-white"></div>
            </div>
            <div class="glass rounded-2xl border border-emerald-500/20 p-5 text-center">
              <div class="text-xs text-[var(--text-muted)] mb-2 uppercase tracking-wider">Break-Even</div>
              <div id="breakEven" class="text-xl font-black text-emerald-400"></div>
            </div>
            <div class="glass rounded-2xl border border-indigo-500/20 p-5 text-center">
              <div class="text-xs text-[var(--text-muted)] mb-2 uppercase tracking-wider">10yr Earnings</div>
              <div id="tenYrEarn" class="text-xl font-black text-indigo-400"></div>
            </div>
            <div class="glass rounded-2xl border border-amber-500/20 p-5 text-center">
              <div class="text-xs text-[var(--text-muted)] mb-2 uppercase tracking-wider">Net Gain</div>
              <div id="netGain" class="text-xl font-black text-amber-400"></div>
            </div>
          </div>

          <!-- Year-wise Projection Table -->
          <div class="glass rounded-2xl border border-[var(--border)] overflow-hidden">
            <div class="px-6 py-4 border-b border-[var(--border)]">
              <h3 class="font-bold text-white flex items-center gap-2">
                <i data-lucide="bar-chart-2" class="w-5 h-5 text-emerald-400"></i> Year-by-Year Earnings Projection
              </h3>
            </div>
            <div class="p-5 space-y-3" id="projectionBars">
              <!-- JS populated -->
            </div>
          </div>

          <!-- CTAs -->
          <div class="grid grid-cols-2 gap-4">
            <a href="colleges.php" class="flex items-center justify-center gap-2 py-3 rounded-xl glass border border-[var(--border)] text-sm font-medium hover:bg-white/5 transition-colors">
              <i data-lucide="search" class="w-4 h-4"></i> Compare Colleges
            </a>
            <a href="scholarships.php" class="flex items-center justify-center gap-2 py-3 rounded-xl btn-primary text-sm font-medium">
              <i data-lucide="award" class="w-4 h-4"></i> Find Scholarships
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

let duration = 4;

function setDuration(yr) {
  duration = yr;
  document.querySelectorAll('.dur-btn').forEach(b => {
    b.classList.remove('border-emerald-500', 'text-emerald-400', 'bg-emerald-500/10');
    b.classList.add('border-[var(--border)]', 'text-[var(--text-secondary)]', 'bg-[var(--bg-secondary)]');
  });
  const btn = document.getElementById('dur_' + yr);
  btn.classList.add('border-emerald-500', 'text-emerald-400', 'bg-emerald-500/10');
  btn.classList.remove('border-[var(--border)]', 'text-[var(--text-secondary)]', 'bg-[var(--bg-secondary)]');
}

function fmt(n) {
  if (n >= 10000000) return '₹' + (n / 10000000).toFixed(1) + 'Cr';
  if (n >= 100000)   return '₹' + (n / 100000).toFixed(1)   + 'L';
  if (n >= 1000)     return '₹' + (n / 1000).toFixed(0)     + 'K';
  return '₹' + n;
}

function updateDisplay() {
  document.getElementById('feesDisplay').textContent   = fmt(+document.getElementById('feesSlider').value);
  document.getElementById('livingDisplay').textContent = fmt(+document.getElementById('livingSlider').value);
  document.getElementById('salaryDisplay').textContent = fmt(+document.getElementById('salarySlider').value);
  document.getElementById('hikeDisplay').textContent   = document.getElementById('hikeSlider').value + '%';
}

function prefillCollege(val) {
  if (!val) return;
  const opt  = document.querySelector(`#collegeSelect option[value="${val}"]`);
  const type = opt ? opt.dataset.type : '';
  // Auto-set typical fees based on type
  const feeMap = { CENTRAL: 800000, GOVERNMENT: 300000, PRIVATE: 1500000, DEEMED: 2000000 };
  const fee = feeMap[type] || 1000000;
  document.getElementById('feesSlider').value = Math.min(fee, 10000000);
  updateDisplay();
}

function calculateROI() {
  const fees    = +document.getElementById('feesSlider').value;
  const living  = +document.getElementById('livingSlider').value;
  const salary0 = +document.getElementById('salarySlider').value;
  const hike    = +document.getElementById('hikeSlider').value / 100;

  const livingTotal = living * 12 * duration;
  const totalInvest = fees + livingTotal;

  // Calculate cumulative earnings vs investment to find break-even year
  let cumEarnings = 0;
  let breakEvenYr = null;
  const projection = [];
  let sal = salary0;

  for (let y = 1; y <= 10; y++) {
    cumEarnings += sal;
    if (!breakEvenYr && cumEarnings >= totalInvest) breakEvenYr = y;
    projection.push({ year: y, salary: sal, cum: cumEarnings });
    sal = Math.round(sal * (1 + hike));
  }

  const tenYrEarnings = projection[9].cum;
  const netGain = tenYrEarnings - totalInvest;
  const roi = ((netGain / totalInvest) * 100).toFixed(0);

  // Verdict
  let verdictTitle, verdictSub, verdictClass, verdictIcon;
  if (!breakEvenYr) {
    verdictTitle = '⚠️ High Risk Investment';
    verdictSub   = 'You may not recover the investment within 10 years. Consider cheaper alternatives or scholarships.';
    verdictClass = 'bg-red-500/10 border-red-500/30';
    verdictIcon  = '🚨';
  } else if (breakEvenYr <= 2) {
    verdictTitle = '🚀 Excellent ROI!';
    verdictSub   = `You\'ll break even in just ${breakEvenYr} year(s)! This is a fantastic investment.`;
    verdictClass = 'bg-emerald-500/10 border-emerald-500/30';
    verdictIcon  = '🌟';
  } else if (breakEvenYr <= 4) {
    verdictTitle = '✅ Good Investment';
    verdictSub   = `Break-even in ${breakEvenYr} years with ${roi}% ROI over 10 years. Solid choice!`;
    verdictClass = 'bg-blue-500/10 border-blue-500/30';
    verdictIcon  = '💼';
  } else {
    verdictTitle = '🟡 Moderate ROI';
    verdictSub   = `Break-even takes ${breakEvenYr} years. Look for scholarships to improve returns.`;
    verdictClass = 'bg-amber-500/10 border-amber-500/30';
    verdictIcon  = '📊';
  }

  // Show results
  document.getElementById('roiInitial').classList.add('hidden');
  const res = document.getElementById('roiResults');
  res.classList.remove('hidden');

  document.getElementById('verdictBanner').className = `rounded-2xl p-5 border flex items-center gap-4 ${verdictClass}`;
  document.getElementById('verdictIcon').textContent  = verdictIcon;
  document.getElementById('verdictTitle').textContent = verdictTitle;
  document.getElementById('verdictSub').textContent   = verdictSub;
  document.getElementById('totalInvest').textContent  = fmt(totalInvest);
  document.getElementById('breakEven').textContent    = breakEvenYr ? breakEvenYr + ' yrs' : '>10 yrs';
  document.getElementById('tenYrEarn').textContent    = fmt(tenYrEarnings);
  document.getElementById('netGain').textContent      = fmt(netGain);

  // Projection bars
  const maxCum = projection[9].cum;
  const bars = projection.map(p => {
    const pct = Math.min(100, (p.cum / maxCum) * 100).toFixed(1);
    const isBreak = p.year === breakEvenYr;
    return `
      <div class="flex items-center gap-3">
        <div class="w-12 text-xs text-[var(--text-muted)] text-right flex-shrink-0">Yr ${p.year}</div>
        <div class="flex-1 h-7 bg-[var(--bg-secondary)] rounded-lg overflow-hidden relative border border-[var(--border)]">
          <div class="h-full rounded-lg transition-all duration-700 ${isBreak ? 'bg-gradient-to-r from-emerald-500 to-teal-500' : 'bg-gradient-to-r from-indigo-500/60 to-purple-500/60'}"
               style="width:${pct}%"></div>
          ${isBreak ? '<div class="absolute inset-0 flex items-center justify-center text-[10px] font-bold text-white">Break Even! 🎉</div>' : ''}
        </div>
        <div class="w-16 text-xs font-bold text-white text-right flex-shrink-0">${fmt(p.cum)}</div>
      </div>`;
  }).join('');

  document.getElementById('projectionBars').innerHTML = bars;
  lucide.createIcons();
}

// Init
setDuration(4);
updateDisplay();
</script>
</body>
</html>
